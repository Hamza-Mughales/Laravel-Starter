<?php

namespace App\Http\Controllers\Relation;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\Phone;
use App\Models\Service;
use App\User;
use Illuminate\Http\Request;

class RelationsController extends Controller
{
  public function hasOneRelation()
  {
    return $user = \App\User::with(['phone' => function ($q) {
      $q->select('code', 'phone', 'user_id');       // to return only specefic fields of phone table
    }])->find(1);

    //return $user -> phone -> code;
    // $phone = $user -> phone;         // this will return the wholl phone table

    return response()->json($user);
  }


  public function hasOneRelationReverse()
  {
    //$phone = Phone::with('user')->find(1);

    $phone = Phone::with(['user' => function ($q) {
      $q->select('id', 'name');
    }])->find(1);

    //make some attribute visible
    $phone->makeVisible(['user_id']);
    //$phone->makeHidden(['code']);
    //return  $phone -> user;  //return user of this phone number
    // get all data  phone + user

    return $phone;
  }

  // Get users those have phones
  public function getUserHasPhone()
  {
    return User::whereHas('phone')->get();
  }


  // Get users those have not phones
  public function getUserNotHasPhone()
  {
    return User::whereDoesntHave('phone')->get();
  }


  // Get users those have phones && where other ondition
  public function getUserWhereHasPhoneWithCondition()
  {
    return User::whereHas('phone', function ($q) {
      $q->where('code', '02');
    })->get();
  }


  ################### one to many relationship mehtods #########

  public function getHospitalDoctors()
  {
    $hospital = Hospital::find(1);  // or Hospital::where('id',1) -> first();  // or Hospital::first();

    // return  $hospital->doctors;   // return hospital doctors

    $hospital = Hospital::with('doctors')->find(1);

    //return $hospital -> name;

    $doctors = $hospital->doctors;

    /* foreach ($doctors as $doctor){
          echo  $doctor -> name.'<br>';
        }*/

    $doctor = Doctor::find(3);

    return $doctor->hospital->name;
  }

  // Get hospitals
  public function hospitals()
  {
    $hospitals = Hospital::select('id', 'name', 'address')->get();
    return view('doctors.hospitals', compact('hospitals'));
  }

  // Get doctors from hospital
  public function doctors($hospital_id)
  {
    $hospital = Hospital::find($hospital_id);
    $doctors = $hospital->doctors;
    return view('doctors.doctors', compact('doctors'));
  }


  // get all hospital which must has doctors
  public function hospitalsHasDoctor()
  {
    return $hospitals = Hospital::whereHas('doctors')->get();
  }

 // Get hospitals Has Only Male  Doctors with the doctors
  public function hospitalsHasOnlyMaleDoctors()
  {
    return $hospitals = Hospital::with('doctors')->whereHas('doctors', function ($q) {
      $q->where('gender', 1);
    })->get();
  }

// Get hiospitals that have no doctors
  public function hospitals_not_has_doctors()
  {
    return Hospital::whereDoesntHave('doctors')->get();
  }

  public function deleteHospital($hospital_id)
  {
    $hospital = Hospital::find($hospital_id);
    if (!$hospital)
      return abort('404');
    //delete doctors in this hospital then delete the hospital
    $hospital->doctors()->delete();
    $hospital->delete();

    //return redirect() -> route('hospital.all');
  }


  // Get the doctor services
  public function getDoctorServices()
  {
    return $doctor = Doctor::with('services')->find(3);
    // return $doctor -> services;
  }

  // Get the doctors for sevice
  public function getServiceDoctors()
  {
    return $doctors = Service::with(['doctors' => function ($q) {
      $q->select('doctors.id', 'name', 'title');
    }])->find(1);
  }


  // Get the doctor's sevice by ID
  public function getDoctorServicesById($doctorId)
  {
    $doctor = Doctor::find($doctorId); // get doctor by ID
    $services = $doctor->services;  // get the doctor services

    $doctors = Doctor::select('id', 'name')->get(); // get all db doctors
    $allServices = Service::select('id', 'name')->get(); // get all db serves

    return view('doctors.services', compact('services', 'doctors', 'allServices'));
  }

  // Save Services to doctors
  public function saveServicesToDoctors(Request $request)
  {
    $doctor = Doctor::find($request->doctor_id); // get the doctor
    if (!$doctor)
      return abort('404');
    // $doctor ->services()-> attach($request -> servicesIds);  // many to many insert to database
    //$doctor ->services()-> sync($request -> servicesIds); // Delete the old vals and add the new
    $doctor->services()->syncWithoutDetaching($request->servicesIds); // add the new without delete the old vals
    return 'success';
  }

  // Get the doctor for patient by using the relation hasOneThrough
  public function getPatientDoctor()
  {
    $patient = Patient::find(2);
    return $patient->doctor;
  }

  // Get the doctor for country by using the relation hasManyThrough
  public function getCountryDoctor()
  {
    $country = Country::find(1);
    return $country->doctors;
  }

  // Get Dotors
  public function getDoctors()
  {
    return $doctors = Doctor::select('id', 'name', 'gender')->get();
    /* if (isset($doctors) && $doctors->count() > 0) {
          foreach ($doctors as $doctor) {
            $doctor->gender = $doctor->gender == 1 ? 'male' : 'female';
            // $doctor -> newVal = 'new';
          }
      }
      return $doctors;*/
  }
}
